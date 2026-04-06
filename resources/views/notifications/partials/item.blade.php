@php
    $isRead = $notification->read_at !== null;
    $isMaster = ($notification->data['is_master'] ?? false) === true;
    $isPending = $isMaster && !$notification->is_dispatched;

    $targetMap = [
        'all'       => ['label' => 'الكل',    'color' => 'gold',    'icon' => 'users'],
        'merchants' => ['label' => 'التجار',  'color' => 'indigo',  'icon' => 'store'],
        'customers' => ['label' => 'العملاء', 'color' => 'emerald', 'icon' => 'user'],
        'staff'     => ['label' => 'الإدارة', 'color' => 'amber',   'icon' => 'shield'],
    ];
    $targetInfo = $targetMap[$notification->target ?? 'all'] ?? $targetMap['all'];

    $iconKey = $notification->data['icon'] ?? 'bell';
    $typeColors = [
        'booking_created'    => 'emerald',
        'booking_confirmed'  => 'emerald',
        'booking_rejected'   => 'rose',
        'merchant_approved'  => 'indigo',
        'system_announcement'=> 'gold',
        'price_alert'        => 'amber',
        'new_message'        => 'sky',
        'broadcast'          => 'gold',
    ];
    $color = $typeColors[$notification->type] ?? 'gold';
@endphp

<div id="notification-{{ $notification->id }}" class="group luxury-card p-0 overflow-hidden border transition-all duration-500
    {{ $isPending ? 'border-amber-500/30 bg-amber-500/5' : ($isRead ? 'border-white/5 dark:border-white/5 opacity-60' : 'border-gold/20 hover:border-gold/40') }}
    rounded-[2rem] shadow-xl {{ $isPending ? 'shadow-amber-500/10' : ($isRead ? '' : 'shadow-gold/5') }} animate-in fade-in slide-in-from-top-4 duration-700">

    <div class="flex items-center gap-8 p-8">

        {{-- Icon --}}
        <div class="flex-shrink-0 relative">
            <div class="w-16 h-16 rounded-2xl bg-{{ $color }}/10 border border-{{ $color }}/20 flex items-center justify-center group-hover:scale-110 transition-transform duration-500 shadow-lg">
                <i data-lucide="{{ $iconKey }}" class="w-8 h-8 text-{{ $color }}-400 dark:text-{{ $color }}-300"></i>
            </div>
            @if(!$isRead && !$isMaster)
                <div class="absolute -top-1.5 -right-1.5 w-4 h-4 bg-gold rounded-full border-2 border-white dark:border-[#1A1A1A] shadow-lg shadow-gold/50 animate-pulse"></div>
            @endif
            @if($isPending)
                <div class="absolute -top-1.5 -right-1.5 w-4 h-4 bg-amber-500 rounded-full border-2 border-white dark:border-[#1A1A1A] shadow-lg shadow-amber-500/50"></div>
            @endif
        </div>

        {{-- Content --}}
        <div class="flex-1 min-w-0 space-y-2">
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <h4 class="text-lg font-black text-main tracking-tight {{ $isRead ? '' : '' }}">
                    {{ $notification->title ?? ($notification->data['title'] ?? __('إشعار جديد')) }}
                    @if(isset($notification->data['count']) && $notification->data['count'] > 1)
                        <span class="mr-2 px-2 py-0.5 bg-gold/20 text-gold text-[10px] rounded-full">{{ $notification->data['count'] }}</span>
                    @endif
                </h4>
                <div class="flex items-center gap-3 flex-shrink-0">
                    {{-- Target Badge (only for master broadcasts) --}}
                    @if($isMaster)
                        <span class="px-3 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest border
                            {{ $targetInfo['color'] === 'gold' ? 'bg-gold/10 text-gold border-gold/20' : 
                               ($targetInfo['color'] === 'indigo' ? 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20' : 
                               ($targetInfo['color'] === 'emerald' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 
                               'bg-amber-500/10 text-amber-400 border-amber-500/20')) }}
                            flex items-center gap-1.5">
                            <i data-lucide="{{ $targetInfo['icon'] }}" class="w-3 h-3"></i>
                            {{ __($targetInfo['label']) }}
                        </span>
                    @endif

                    {{-- Status Badge --}}
                    @if($isPending)
                        <span class="px-3 py-1.5 bg-amber-500/10 border border-amber-500/20 text-amber-400 text-[9px] font-black uppercase tracking-widest rounded-full flex items-center gap-1.5">
                            <i data-lucide="clock" class="w-3 h-3"></i>
                            {{ __('مجدول') }}
                        </span>
                    @elseif($isRead)
                        <span class="px-3 py-1.5 bg-white/5 dark:bg-white/5 border border-main/10 text-muted/30 text-[9px] font-black uppercase tracking-widest rounded-full flex items-center gap-1.5">
                            <i data-lucide="check-check" class="w-3 h-3"></i>
                            {{ __('مقروء') }}
                        </span>
                    @else
                        <span class="px-3 py-1.5 bg-gold/10 border border-gold/20 text-gold text-[9px] font-black uppercase tracking-widest rounded-full flex items-center gap-1.5 animate-pulse">
                            <span class="w-1.5 h-1.5 rounded-full bg-gold"></span>
                            {{ __('جديد') }}
                        </span>
                    @endif
                </div>
            </div>

            <p class="text-sm text-muted/50 font-medium leading-relaxed max-w-3xl">
                {{ $notification->message ?? ($notification->data['message'] ?? '') }}
            </p>

            <div class="flex items-center gap-4 pt-1 flex-wrap">
                <span class="text-[9px] text-muted/30 font-black uppercase tracking-widest flex items-center gap-1.5">
                    <i data-lucide="clock" class="w-3 h-3"></i>
                    {{ $notification->created_at->diffForHumans() }}
                </span>
                @if($isPending && $notification->scheduled_at)
                    <span class="text-[9px] text-amber-400 font-black uppercase tracking-widest flex items-center gap-1.5">
                        <i data-lucide="calendar" class="w-3 h-3"></i>
                        {{ __('موعد الإرسال') }}: {{ $notification->scheduled_at->format('Y/m/d H:i') }}
                    </span>
                @endif
                @if($isMaster)
                    <span class="text-[9px] text-muted/30 font-black uppercase tracking-widest flex items-center gap-1.5">
                        <i data-lucide="megaphone" class="w-3 h-3 text-gold/40"></i>
                        {{ __('إشعار جماعي') }}
                    </span>
                @endif
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-3 flex-shrink-0">
            @if(!$isRead && !$isMaster)
                <form action="{{ route('notifications.read', $notification->id) }}" method="POST" class="mark-read-form">
                    @csrf
                    <button type="submit"
                        class="w-11 h-11 bg-white dark:bg-black border border-gold/10 rounded-xl text-muted/40 hover:text-emerald-500 hover:bg-emerald-500/10 hover:border-emerald-500/30 transition-all flex items-center justify-center shadow-sm"
                        title="{{ __('تحديد كمقروء') }}">
                        <i data-lucide="check" class="w-4 h-4"></i>
                    </button>
                </form>
            @endif
            <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST"
                  onsubmit="return confirm('{{ __('حذف هذا الإشعار؟') }}')">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="w-11 h-11 bg-rose-500/5 border border-rose-500/10 rounded-xl text-rose-500/40 hover:bg-rose-500 hover:text-white transition-all flex items-center justify-center shadow-sm"
                    title="{{ __('حذف') }}">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            </form>
        </div>

    </div>
</div>
