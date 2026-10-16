@extends('layouts.admin')

@section('title', __('المسوقون'))

@section('content')
<div class="space-y-10 pb-20" dir="rtl">

    {{-- ─── Header ─────────────────────────────────── --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-8 pt-4 pb-8 border-b border-gold/10 relative">
        <div class="absolute right-0 top-0 w-40 h-40 bg-gold/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative">
            <h2 class="text-4xl lg:text-5xl font-black text-main uppercase tracking-[0.15em] leading-tight flex items-center gap-4">
                {{ __('المسوقون') }}
                <div class="w-12 h-12 rounded-2xl bg-gold/10 border border-gold/20 flex items-center justify-center shadow-lg shadow-gold/10">
                    <i data-lucide="megaphone" class="w-6 h-6 text-gold"></i>
                </div>
            </h2>
            <div class="flex items-center gap-3 mt-4">
                <div class="h-1.5 w-20 bg-gold shadow-[0_0_20px_rgba(212,175,55,0.6)] rounded-full"></div>
                <div class="h-1.5 w-6 bg-gold/30 rounded-full"></div>
                <p class="text-[10px] text-muted/60 font-black uppercase tracking-[0.3em] mr-2">{{ __('إدارة المسوقين والعمولات') }}</p>
            </div>
        </div>
        <a href="{{ route('marketers.create') }}"
           class="flex items-center gap-2 px-6 py-3.5 rounded-2xl bg-gradient-to-tr from-[#C5A059] to-[#E8D095] text-onyx font-black text-sm shadow-lg shadow-gold/30 hover:shadow-gold/50 transition-all duration-300 active:scale-95">
            <i data-lucide="plus-circle" class="w-5 h-5"></i>
            {{ __('إضافة مسوق جديد') }}
        </a>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="flex items-center gap-3 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 font-bold text-sm">
            <i data-lucide="check-circle" class="w-5 h-5 flex-shrink-0"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- ─── Filters ─────────────────────────────────── --}}
    <form method="GET" class="flex flex-wrap gap-3 items-center">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="{{ __('بحث بالاسم أو الهاتف أو الرمز...') }}"
               class="px-5 py-3 rounded-2xl bg-white/60 dark:bg-black/30 border border-gold/20 focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none text-main font-bold text-sm flex-1 min-w-[220px] transition-all">
        <select name="status" class="px-4 py-3 rounded-2xl bg-white/60 dark:bg-black/30 border border-gold/20 focus:border-gold outline-none text-main font-bold text-sm transition-all">
            <option value="">{{ __('كل الحالات') }}</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('نشط') }}</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>{{ __('موقوف') }}</option>
        </select>
        <button type="submit" class="flex items-center gap-2 px-5 py-3 rounded-2xl bg-gold/10 border border-gold/30 text-gold font-black text-sm hover:bg-gold/20 transition-all">
            <i data-lucide="search" class="w-4 h-4"></i>
            {{ __('بحث') }}
        </button>
        @if(request('search') || request('status'))
            <a href="{{ route('marketers.index') }}" class="text-xs font-black text-muted/50 hover:text-muted transition-colors px-3 py-3">
                {{ __('مسح الفلاتر') }}
            </a>
        @endif
    </form>

    {{-- ─── Stats Cards ─────────────────────────────── --}}
    @php
        $totalMarketers  = \App\Models\Marketer::count();
        $activeMarketers = \App\Models\Marketer::where('status', 'active')->count();
        $totalEarned     = \App\Models\Marketer::sum('total_earned');
        $totalPaid       = \App\Models\Marketer::sum('total_paid');
        $pendingBalance  = $totalEarned - $totalPaid;
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['label' => 'إجمالي المسوقين', 'value' => $totalMarketers, 'icon' => 'users', 'color' => 'gold'],
            ['label' => 'مسوقون نشطون',    'value' => $activeMarketers, 'icon' => 'user-check', 'color' => 'emerald-500'],
            ['label' => 'إجمالي العمولات', 'value' => number_format($totalEarned, 2), 'icon' => 'wallet', 'color' => 'blue-500'],
            ['label' => 'المبالغ المعلقة',  'value' => number_format($pendingBalance, 2), 'icon' => 'clock', 'color' => 'amber-500'],
        ] as $stat)
        <div class="bg-white/60 dark:bg-[#1A1A1A]/80 backdrop-blur-xl border border-gold/15 rounded-[1.5rem] p-5 shadow-lg relative overflow-hidden group hover:border-gold/30 transition-all">
            <div class="absolute inset-0 bg-gradient-to-br from-gold/3 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-[10px] text-muted/50 font-black uppercase tracking-widest">{{ $stat['label'] }}</p>
                    <p class="text-2xl font-black text-main mt-1">{{ $stat['value'] }}</p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-{{ $stat['color'] }}/10 border border-{{ $stat['color'] }}/20 flex items-center justify-center">
                    <i data-lucide="{{ $stat['icon'] }}" class="w-5 h-5 text-{{ $stat['color'] }}"></i>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ─── Marketers Table ─────────────────────────── --}}
    <div class="bg-white/60 dark:bg-[#1A1A1A]/80 backdrop-blur-xl border border-gold/15 rounded-[2rem] p-6 shadow-xl overflow-hidden">
        @if($marketers->isEmpty())
            <div class="text-center py-24">
                <div class="w-20 h-20 rounded-full bg-gold/5 border border-gold/10 flex items-center justify-center mx-auto mb-6">
                    <i data-lucide="megaphone" class="w-10 h-10 text-gold/40"></i>
                </div>
                <p class="text-muted/50 font-black text-sm">{{ __('لا يوجد مسوقون بعد') }}</p>
                <a href="{{ route('marketers.create') }}" class="inline-flex items-center gap-2 mt-4 text-gold font-black text-xs hover:underline">
                    <i data-lucide="plus" class="w-4 h-4"></i> {{ __('إضافة أول مسوق') }}
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-[10px] font-black text-muted/50 uppercase tracking-widest border-b border-gold/10">
                            <th class="pb-4 text-right px-3">{{ __('المسوق') }}</th>
                            <th class="pb-4 text-right px-3">{{ __('رمز الإحالة') }}</th>
                            <th class="pb-4 text-right px-3">{{ __('نوع الحساب') }}</th>
                            <th class="pb-4 text-right px-3">{{ __('العمولة') }}</th>
                            <th class="pb-4 text-right px-3">{{ __('العملة') }}</th>
                            <th class="pb-4 text-right px-3">{{ __('مكتسب') }}</th>
                            <th class="pb-4 text-right px-3">{{ __('معلق') }}</th>
                            <th class="pb-4 text-right px-3">{{ __('المستخدمون') }}</th>
                            <th class="pb-4 text-right px-3">{{ __('الحالة') }}</th>
                            <th class="pb-4 text-center px-3">{{ __('إجراءات') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gold/5">
                        @foreach($marketers as $marketer)
                        <tr class="hover:bg-gold/3 transition-colors group">
                            <td class="py-4 px-3">
                                <div>
                                    <p class="font-black text-main">{{ $marketer->name }}</p>
                                    <p class="text-xs text-muted/50 mt-0.5">{{ $marketer->phone }}</p>
                                    @if($marketer->email)
                                        <p class="text-xs text-muted/40">{{ $marketer->email }}</p>
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 px-3">
                                <div class="flex items-center gap-2">
                                    <code class="text-xs bg-gold/10 border border-gold/20 text-gold px-3 py-1.5 rounded-lg font-black tracking-widest">
                                        {{ $marketer->code }}
                                    </code>
                                    <button onclick="navigator.clipboard.writeText('{{ $marketer->code }}').then(()=>alert('تم نسخ الرمز!'))"
                                            class="text-muted/30 hover:text-gold transition-colors">
                                        <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                                    </button>
                                </div>
                            </td>
                            <td class="py-4 px-3">
                                @php
                                    $typeMap = ['merchant' => ['تجار','blue-500'], 'customer' => ['عملاء','emerald-500'], 'both' => ['كلاهما','gold']];
                                    [$typeLabel, $typeColor] = $typeMap[$marketer->target_type] ?? ['—','muted'];
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black bg-{{ $typeColor }}/10 text-{{ $typeColor }} border border-{{ $typeColor }}/20">
                                    {{ $typeLabel }}
                                </span>
                            </td>
                            <td class="py-4 px-3">
                                @if($marketer->target_type !== 'customer')
                                    <p class="text-xs font-bold text-main">تاجر: <span class="text-gold font-black">{{ $marketer->commission_per_merchant }}</span></p>
                                @endif
                                @if($marketer->target_type !== 'merchant')
                                    <p class="text-xs font-bold text-main">عميل: <span class="text-gold font-black">{{ $marketer->commission_per_customer }}</span></p>
                                @endif
                            </td>
                            <td class="py-4 px-3">
                                <span class="text-xs font-black text-main">{{ $marketer->currency }}</span>
                            </td>
                            <td class="py-4 px-3">
                                <span class="font-black text-main">{{ number_format($marketer->total_earned, 2) }}</span>
                            </td>
                            <td class="py-4 px-3">
                                @php $pending = $marketer->total_earned - $marketer->total_paid; @endphp
                                <span class="font-black {{ $pending > 0 ? 'text-amber-500' : 'text-muted/40' }}">
                                    {{ number_format($pending, 2) }}
                                </span>
                            </td>
                            <td class="py-4 px-3">
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-black bg-blue-500/10 text-blue-500 border border-blue-500/20">
                                    <i data-lucide="users" class="w-3 h-3"></i>
                                    {{ $marketer->commissions_count }}
                                </span>
                            </td>
                            <td class="py-4 px-3">
                                <form method="POST" action="{{ route('marketers.toggle', $marketer->id) }}">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[10px] font-black border transition-all
                                        {{ $marketer->status === 'active'
                                            ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-500/20'
                                            : 'bg-rose-500/10 border-rose-500/30 text-rose-600 dark:text-rose-400 hover:bg-rose-500/20' }}">
                                        <i data-lucide="{{ $marketer->status === 'active' ? 'check-circle' : 'x-circle' }}" class="w-3.5 h-3.5"></i>
                                        {{ $marketer->status === 'active' ? __('نشط') : __('موقوف') }}
                                    </button>
                                </form>
                            </td>
                            <td class="py-4 px-3">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('marketers.show', $marketer->id) }}"
                                       class="p-2 rounded-xl bg-blue-500/10 border border-blue-500/20 text-blue-500 hover:bg-blue-500/20 transition-all" title="التفاصيل">
                                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                    </a>
                                    <a href="{{ route('marketers.edit', $marketer->id) }}"
                                       class="p-2 rounded-xl bg-gold/10 border border-gold/20 text-gold hover:bg-gold/20 transition-all" title="تعديل">
                                        <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                                    </a>
                                    <form method="POST" action="{{ route('marketers.destroy', $marketer->id) }}"
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذا المسوق؟')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-500 hover:bg-rose-500/20 transition-all" title="حذف">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-6">{{ $marketers->links() }}</div>
        @endif
    </div>
</div>
@endsection
