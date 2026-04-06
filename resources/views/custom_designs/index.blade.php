@extends('layouts.admin')

@section('title', __('طلبات التصميم الخاص'))

@section('content')
<div class="space-y-12 pb-20" dir="rtl">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">
        <div>
            <h2 class="text-4xl font-black text-main uppercase tracking-widest mb-4">{{ __('طلبات التفصيل') }}</h2>
            <div class="h-1.5 w-24 bg-gold shadow-[0_0_15px_rgba(212,175,55,0.4)] rounded-full"></div>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="luxury-card p-8 border border-main/10 bg-main/5 flex flex-col items-center justify-center text-center">
            <p class="text-[10px] text-muted/40 font-black uppercase tracking-widest mb-2">{{ __('طلبات جديدة') }}</p>
            <h3 class="text-4xl font-black text-gold">{{ count($designs->where('status', 'pending')) }}</h3>
        </div>
        <div class="luxury-card p-8 border border-main/10 bg-main/5 flex flex-col items-center justify-center text-center">
            <p class="text-[10px] text-muted/40 font-black uppercase tracking-widest mb-2">{{ __('تحت التنفيذ') }}</p>
            <h3 class="text-4xl font-black text-main">{{ count($designs->where('status', 'processing')) }}</h3>
        </div>
    </div>

    <!-- Design Table -->
    <div class="luxury-card overflow-hidden border border-main/10">
        <div class="overflow-x-auto">
            <table class="w-full text-right border-collapse">
                <thead>
                    <tr class="bg-main/5 border-b border-main/10 text-muted/60 text-[10px] font-black uppercase tracking-widest">
                        <th class="px-8 py-6">{{ __('رقم الطلب') }}</th>
                        <th class="px-8 py-6">{{ __('العميل') }}</th>
                        <th class="px-8 py-6">{{ __('نوع التصميم') }}</th>
                        <th class="px-8 py-6">{{ __('الميزانية') }}</th>
                        <th class="px-8 py-6">{{ __('الحالة') }}</th>
                        <th class="px-8 py-6 text-center">{{ __('الإجراءات') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-main/5 text-sm">
                    @forelse($designs as $design)
                        <tr class="group hover:bg-main/5 transition-all">
                            <td class="px-8 py-6">
                                <span class="text-gold font-black">#DS-{{ $design->id }}</span>
                            </td>
                            <td class="px-8 py-6 text-main font-bold">
                                <div>
                                    <div class="text-base">{{ $design->user->name ?? __('غير مسجل') }}</div>
                                    <div class="text-[10px] text-muted/40 font-medium">{{ $design->user->phone ?? '-' }}</div>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-main font-bold">
                                {{ __($design->design_type ?? 'خاتم') }}
                            </td>
                            <td class="px-8 py-6 font-black text-main">
                                {{ number_format($design->budget ?? 0) }} <span class="text-[9px] text-muted/40">{{ __('ريال') }}</span>
                            </td>
                            <td class="px-8 py-6">
                                @php
                                    $statusColor = match($design->status) {
                                        'completed' => 'emerald',
                                        'processing' => 'indigo',
                                        'pending' => 'amber',
                                        'rejected' => 'rose',
                                        default => 'slate'
                                    };
                                @endphp
                                <span class="px-3 py-1 bg-{{ $statusColor }}-500/10 border border-{{ $statusColor }}-500/20 text-{{ $statusColor }}-500 text-[9px] font-black uppercase rounded-full">
                                    {{ __($design->status) }}
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center justify-center gap-3">
                                    <a href="{{ route('custom_designs.show', $design->id) }}" class="p-2.5 bg-card border border-main rounded-xl text-muted/40 hover:text-gold transition-all" title="{{ __('عرض') }}">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    <form action="{{ route('custom_designs.destroy', $design->id) }}" method="POST" onsubmit="return confirm('{{ __('حذف الطلب؟') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2.5 bg-card border border-main rounded-xl text-muted/40 hover:text-rose-500 transition-all">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-20 text-center text-muted/20 font-black uppercase tracking-widest italic">
                                {{ __('لا توجد طلبات تصميم خاص حالياً') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
