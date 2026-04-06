@extends('layouts.admin')

@section('title', __('تفاصيل طلب التصميم'))

@section('content')
<div class="space-y-12 pb-20" dir="rtl">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">
        <div>
            <h2 class="text-4xl font-black text-main uppercase tracking-widest mb-4">{{ __('تفاصيل الطلب الخاص') }}</h2>
            <div class="h-1.5 w-24 bg-gold shadow-[0_0_15px_rgba(212,175,55,0.4)] rounded-full"></div>
        </div>
        
        <div class="flex flex-wrap items-center gap-4">
            <a href="{{ route('custom_designs.index') }}" class="px-8 py-4 bg-card border border-main text-muted/40 rounded-xl text-[10px] font-black uppercase tracking-widest flex items-center gap-3 hover:text-gold transition-all">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                {{ __('العودة للطلبات') }}
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="p-6 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 rounded-3xl font-black text-sm text-center">
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
        <!-- Visualization Section -->
        <div class="space-y-8">
            <div class="luxury-card p-4 border border-main/10 bg-main/5 aspect-square relative overflow-hidden group">
                @if($design->image_url)
                    <img src="{{ $design->image_url }}" class="w-full h-full object-cover rounded-2xl group-hover:scale-110 transition-transform duration-700">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-gold/5 text-gold/10">
                        <i data-lucide="image" class="w-32 h-32"></i>
                    </div>
                @endif
                
                <div class="absolute top-8 right-8 px-5 py-2 bg-black/60 backdrop-blur-md border border-white/10 rounded-full text-xs font-black text-gold uppercase tracking-widest">
                    {{ $design->design_type }}
                </div>
            </div>
        </div>

        <!-- Details Section -->
        <div class="space-y-8">
            <div class="luxury-card p-12 border border-main/10 bg-main/5 space-y-10">
                <div class="flex items-center justify-between">
                    <div class="space-y-1">
                        <h3 class="text-3xl font-black text-main uppercase tracking-widest">{{ __('رقم الطلب') }}</h3>
                        <p class="text-gold font-black">#DS-{{ $design->id }}</p>
                    </div>
                    @php
                        $statusColor = match($design->status) {
                            'completed' => 'emerald',
                            'processing' => 'indigo',
                            'pending' => 'amber',
                            'rejected' => 'rose',
                            default => 'slate'
                        };
                    @endphp
                    <span class="px-6 py-2 bg-{{ $statusColor }}-500/10 border border-{{ $statusColor }}-500/20 text-{{ $statusColor }}-500 text-[11px] font-black uppercase rounded-full">
                        {{ __($design->status) }}
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-8 py-8 border-y border-main/10 text-right">
                    <div class="space-y-1">
                        <p class="text-[10px] text-muted/40 font-black uppercase tracking-widest">{{ __('الميزانية المقررة') }}</p>
                        <p class="text-3xl font-black text-main">{{ number_format($design->budget ?? 0) }} <span class="text-sm font-medium opacity-40">MRU</span></p>
                    </div>
                    <div class="space-y-1 text-left">
                        <p class="text-[10px] text-muted/40 font-black uppercase tracking-widest">{{ __('تاريخ الطلب') }}</p>
                        <p class="text-xl font-black text-main">{{ $design->created_at->format('Y/m/d') }}</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <h4 class="text-sm font-black text-main uppercase tracking-widest flex items-center gap-3">
                        <i data-lucide="info" class="w-4 h-4 text-gold"></i>
                        {{ __('متطلبات العميل') }}
                    </h4>
                    <p class="text-sm text-muted/60 leading-relaxed font-bold">{{ $design->requirements ?? __('لم يتم إدراج متطلبات خاصة حالياً.') }}</p>
                </div>

                <div class="pt-10 flex flex-col md:flex-row md:items-center justify-between gap-8">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-gold/5 flex items-center justify-center text-lg font-black text-gold border border-gold/10">
                            {{ substr($design->user->name ?? '?', 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-black text-main">{{ $design->user->name ?? __('ضيف') }}</p>
                            <p class="text-[10px] text-muted/40 font-medium">{{ $design->user->phone ?? '-' }}</p>
                        </div>
                    </div>
                    
                    <form action="{{ route('custom_designs.updateStatus', $design->id) }}" method="POST" class="flex items-center gap-4">
                        @csrf
                        @method('PATCH')
                        <select name="status" class="input-luxury pr-8 pl-12 py-4 text-xs font-black uppercase tracking-widest">
                            <option value="pending" {{ $design->status == 'pending' ? 'selected' : '' }}>{{ __('قيد المراجعة') }}</option>
                            <option value="processing" {{ $design->status == 'processing' ? 'selected' : '' }}>{{ __('تحت التنفيذ') }}</option>
                            <option value="completed" {{ $design->status == 'completed' ? 'selected' : '' }}>{{ __('مكتمل') }}</option>
                            <option value="rejected" {{ $design->status == 'rejected' ? 'selected' : '' }}>{{ __('مرفوض') }}</option>
                        </select>
                        <button type="submit" class="p-4 bg-gold text-onyx rounded-xl hover:scale-105 transition-all">
                            <i data-lucide="check" class="w-5 h-5"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
