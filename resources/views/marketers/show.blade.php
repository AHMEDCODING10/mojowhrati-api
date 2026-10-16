@extends('layouts.admin')

@section('title', __('تفاصيل المسوق') . ' — ' . $marketer->name)

@section('content')
<div class="space-y-10 pb-20" dir="rtl">

    {{-- Header --}}
    <div class="pt-4 pb-8 border-b border-gold/10">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('marketers.index') }}" class="w-10 h-10 rounded-xl bg-gold/10 border border-gold/20 flex items-center justify-center hover:bg-gold/20 transition-all">
                    <i data-lucide="arrow-right" class="w-5 h-5 text-gold"></i>
                </a>
                <div>
                    <h2 class="text-3xl font-black text-main flex items-center gap-3">
                        {{ $marketer->name }}
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black
                            {{ $marketer->status === 'active' ? 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-500 border border-rose-500/20' }}">
                            {{ $marketer->status === 'active' ? __('نشط') : __('موقوف') }}
                        </span>
                    </h2>
                    <p class="text-xs text-muted/50 font-bold mt-1">{{ $marketer->phone }} @if($marketer->email) • {{ $marketer->email }} @endif</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('marketers.edit', $marketer->id) }}"
                   class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gold/10 border border-gold/20 text-gold font-black text-xs hover:bg-gold/20 transition-all">
                    <i data-lucide="pencil" class="w-4 h-4"></i> {{ __('تعديل') }}
                </a>
                <form method="POST" action="{{ route('marketers.mark-paid', $marketer->id) }}">
                    @csrf
                    <button type="submit"
                            onclick="return confirm('سيتم تحويل جميع العمولات المعلقة إلى مدفوعة. متأكد؟')"
                            class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 font-black text-xs hover:bg-emerald-500/20 transition-all">
                        <i data-lucide="check-check" class="w-4 h-4"></i> {{ __('تسديد العمولات المعلقة') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="flex items-center gap-3 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-600 dark:text-emerald-400 font-bold text-sm">
            <i data-lucide="check-circle" class="w-5 h-5 flex-shrink-0"></i> {{ session('success') }}
        </div>
    @endif

    {{-- ─── Stats Cards ──────────────────────────── --}}
    @php
        $pending = $marketer->total_earned - $marketer->total_paid;
    @endphp
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        @foreach([
            ['label' => 'رمز الإحالة', 'value' => $marketer->code, 'icon' => 'hash', 'mono' => true],
            ['label' => 'نوع الاستهداف', 'value' => ['merchant'=>'تجار','customer'=>'عملاء','both'=>'كلاهما'][$marketer->target_type], 'icon' => 'target'],
            ['label' => 'إجمالي المكتسب', 'value' => number_format($marketer->total_earned, 2) . ' ' . $marketer->currency, 'icon' => 'wallet'],
            ['label' => 'إجمالي المدفوع', 'value' => number_format($marketer->total_paid, 2) . ' ' . $marketer->currency, 'icon' => 'banknote'],
            ['label' => 'المبلغ المعلق', 'value' => number_format($pending, 2) . ' ' . $marketer->currency, 'icon' => 'clock'],
        ] as $stat)
        <div class="bg-white/60 dark:bg-[#1A1A1A]/80 backdrop-blur-xl border border-gold/15 rounded-[1.5rem] p-5 shadow-lg">
            <p class="text-[9px] text-muted/50 font-black uppercase tracking-widest mb-1">{{ $stat['label'] }}</p>
            <p class="text-lg font-black text-main {{ isset($stat['mono']) ? 'tracking-[0.15em] text-gold' : '' }}">{{ $stat['value'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- ─── Commission Rates ────────────────────── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @if($marketer->target_type !== 'customer')
        <div class="bg-blue-500/5 border border-blue-500/15 rounded-[1.5rem] p-6 flex items-center gap-5">
            <div class="w-14 h-14 rounded-2xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center flex-shrink-0">
                <i data-lucide="store" class="w-7 h-7 text-blue-500"></i>
            </div>
            <div>
                <p class="text-xs text-muted/50 font-black">{{ __('عمولة إضافة تاجر') }}</p>
                <p class="text-2xl font-black text-main">{{ $marketer->commission_per_merchant }} <span class="text-sm text-muted/40">{{ $marketer->currency }}</span></p>
            </div>
        </div>
        @endif
        @if($marketer->target_type !== 'merchant')
        <div class="bg-emerald-500/5 border border-emerald-500/15 rounded-[1.5rem] p-6 flex items-center gap-5">
            <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center flex-shrink-0">
                <i data-lucide="users" class="w-7 h-7 text-emerald-500"></i>
            </div>
            <div>
                <p class="text-xs text-muted/50 font-black">{{ __('عمولة إضافة عميل') }}</p>
                <p class="text-2xl font-black text-main">{{ $marketer->commission_per_customer }} <span class="text-sm text-muted/40">{{ $marketer->currency }}</span></p>
            </div>
        </div>
        @endif
    </div>

    {{-- ─── Users Added by Marketer ──────────────── --}}
    <div class="bg-white/60 dark:bg-[#1A1A1A]/80 backdrop-blur-xl border border-gold/15 rounded-[2rem] p-6 shadow-xl">
        <div class="flex items-center gap-3 mb-6">
            <i data-lucide="users" class="w-5 h-5 text-gold"></i>
            <h4 class="text-base font-black text-main">{{ __('المستخدمون المُضافون بواسطة هذا المسوق') }}</h4>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black bg-gold/10 text-gold border border-gold/20">
                {{ $users->count() }}
            </span>
        </div>

        @if($users->isEmpty())
            <div class="text-center py-12">
                <div class="w-16 h-16 rounded-full bg-gold/5 border border-gold/10 flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="user-x" class="w-8 h-8 text-gold/30"></i>
                </div>
                <p class="text-muted/40 text-sm font-bold">{{ __('لم يتم إضافة أي مستخدم بعد عبر هذا المسوق') }}</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-[10px] font-black text-muted/50 uppercase tracking-widest border-b border-gold/10">
                            <th class="pb-3 text-right px-3">{{ __('المستخدم') }}</th>
                            <th class="pb-3 text-right px-3">{{ __('الدور') }}</th>
                            <th class="pb-3 text-right px-3">{{ __('الهاتف') }}</th>
                            <th class="pb-3 text-right px-3">{{ __('تاريخ التسجيل') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gold/5">
                        @foreach($users as $user)
                        <tr class="hover:bg-gold/3 transition-colors">
                            <td class="py-3 px-3">
                                <a href="{{ route('users.show', $user->id) }}" class="font-black text-main hover:text-gold transition-colors">
                                    {{ $user->name }}
                                </a>
                            </td>
                            <td class="py-3 px-3">
                                <span class="inline-flex px-2.5 py-0.5 rounded-lg text-[10px] font-black
                                    {{ $user->role === 'merchant' ? 'bg-blue-500/10 text-blue-500' : 'bg-emerald-500/10 text-emerald-500' }}">
                                    {{ $user->role === 'merchant' ? 'تاجر' : 'عميل' }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-muted/60 font-bold text-xs" dir="ltr">{{ $user->phone }}</td>
                            <td class="py-3 px-3 text-muted/50 text-xs">{{ $user->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- ─── Commission Log ───────────────────────── --}}
    <div class="bg-white/60 dark:bg-[#1A1A1A]/80 backdrop-blur-xl border border-gold/15 rounded-[2rem] p-6 shadow-xl">
        <div class="flex items-center gap-3 mb-6">
            <i data-lucide="receipt" class="w-5 h-5 text-gold"></i>
            <h4 class="text-base font-black text-main">{{ __('سجل العمولات') }}</h4>
        </div>

        @if($commissions->isEmpty())
            <div class="text-center py-12">
                <div class="w-16 h-16 rounded-full bg-gold/5 border border-gold/10 flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="receipt" class="w-8 h-8 text-gold/30"></i>
                </div>
                <p class="text-muted/40 text-sm font-bold">{{ __('لا توجد عمولات مسجلة بعد') }}</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-[10px] font-black text-muted/50 uppercase tracking-widest border-b border-gold/10">
                            <th class="pb-3 text-right px-3">{{ __('المستخدم') }}</th>
                            <th class="pb-3 text-right px-3">{{ __('نوع الحساب') }}</th>
                            <th class="pb-3 text-right px-3">{{ __('المبلغ') }}</th>
                            <th class="pb-3 text-right px-3">{{ __('الحالة') }}</th>
                            <th class="pb-3 text-right px-3">{{ __('التاريخ') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gold/5">
                        @foreach($commissions as $comm)
                        <tr class="hover:bg-gold/3 transition-colors">
                            <td class="py-3 px-3">
                                <span class="font-bold text-main">{{ $comm->user->name ?? '—' }}</span>
                            </td>
                            <td class="py-3 px-3">
                                <span class="inline-flex px-2.5 py-0.5 rounded-lg text-[10px] font-black
                                    {{ $comm->user_type === 'merchant' ? 'bg-blue-500/10 text-blue-500' : 'bg-emerald-500/10 text-emerald-500' }}">
                                    {{ $comm->user_type === 'merchant' ? 'تاجر' : 'عميل' }}
                                </span>
                            </td>
                            <td class="py-3 px-3 font-black text-gold">{{ number_format($comm->amount, 2) }} {{ $comm->currency }}</td>
                            <td class="py-3 px-3">
                                <span class="inline-flex px-2.5 py-0.5 rounded-lg text-[10px] font-black
                                    {{ $comm->status === 'paid' ? 'bg-emerald-500/10 text-emerald-500' : 'bg-amber-500/10 text-amber-500' }}">
                                    {{ $comm->status === 'paid' ? 'مدفوعة' : 'معلقة' }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-muted/50 text-xs">{{ $comm->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-6">{{ $commissions->links() }}</div>
        @endif
    </div>

    {{-- Notes --}}
    @if($marketer->notes)
    <div class="bg-white/60 dark:bg-[#1A1A1A]/80 backdrop-blur-xl border border-gold/15 rounded-[2rem] p-6 shadow-xl">
        <div class="flex items-center gap-3 mb-4">
            <i data-lucide="notebook" class="w-5 h-5 text-gold"></i>
            <h4 class="text-base font-black text-main">{{ __('ملاحظات') }}</h4>
        </div>
        <p class="text-sm text-muted/70 font-bold leading-relaxed whitespace-pre-line">{{ $marketer->notes }}</p>
    </div>
    @endif
</div>
@endsection
