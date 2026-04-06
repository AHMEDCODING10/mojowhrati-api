@extends('layouts.admin')

@section('title', __('نتائج البحث'))

@section('content')
<div class="space-y-12 pb-20" dir="rtl">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">
        <div>
            <h2 class="text-4xl font-black text-main uppercase tracking-widest mb-4">
                {{ __('نتائج البحث عن') }}: <span class="text-gold italic">"{{ $query }}"</span>
            </h2>
            <div class="h-1.5 w-24 bg-gold shadow-[0_0_15px_rgba(212,175,55,0.4)] rounded-full"></div>
        </div>
        
        <div class="flex flex-wrap items-center gap-4">
            <span class="px-6 py-3 bg-card border border-main text-muted/40 rounded-xl text-[10px] font-black uppercase tracking-widest">
                {{ $totalResults ?? 0 }} {{ __('نتيجة تم العثور عليها') }}
            </span>
        </div>
    </div>

    <!-- Search Tabs -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach(['products' => 'gem', 'users' => 'users', 'merchants' => 'store'] as $type => $icon)
            @php $count = count($results[$type] ?? []); @endphp
            <div class="luxury-card p-8 border {{ $count > 0 ? 'border-gold/30 bg-gold/5' : 'border-main/10 bg-main/5 opacity-40' }} flex items-center justify-between group transition-all duration-500">
                <div class="flex items-center gap-6">
                    <div class="w-12 h-12 rounded-xl bg-gold/10 border border-gold/20 flex items-center justify-center text-gold group-hover:scale-110 transition-transform duration-500">
                        <i data-lucide="{{ $icon }}" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h4 class="text-lg font-black text-main uppercase tracking-widest">{{ __($type) }}</h4>
                        <p class="text-[9px] text-muted/40 font-black uppercase tracking-widest mt-1">{{ $count }} {{ __('عنصر') }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Detailed Results Grid -->
    <div class="space-y-10">
        @foreach(['products', 'users', 'merchants'] as $type)
            @if(count($results[$type] ?? []) > 0)
                <div class="space-y-6">
                    <h3 class="text-xl font-black text-main flex items-center gap-4 uppercase tracking-[0.2em]">
                        <span class="w-8 h-px bg-gold"></span>
                        {{ __($type) }}
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($results[$type] as $item)
                            <div class="luxury-card p-6 border border-main/10 group hover:border-gold/30 hover:bg-main/10 transition-all duration-300">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-lg bg-gold/5 flex items-center justify-center text-gold/30 group-hover:scale-110 transition-transform">
                                        <i data-lucide="{{ $type == 'products' ? 'gem' : ($type == 'users' ? 'user' : 'store') }}" class="w-6 h-6"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h5 class="text-sm font-black text-main truncate group-hover:text-gold transition-colors">{{ $item->title ?? $item->name ?? $item->store_name }}</h5>
                                        <p class="text-[10px] text-muted/40 truncate mt-0.5">{{ $item->email ?? $item->description ?? '' }}</p>
                                    </div>
                                    <a href="{{ route($type.'.show', $item->id) }}" class="p-2 bg-card border border-main rounded-lg text-muted/20 hover:text-gold transition-all">
                                        <i data-lucide="arrow-left" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    @if(($totalResults ?? 0) === 0)
        <div class="luxury-card p-24 text-center space-y-8 border border-dashed border-main/20 bg-main/5">
            <div class="w-24 h-24 bg-gold/5 rounded-full flex items-center justify-center mx-auto text-gold/10 border border-gold/5 opacity-30">
                <i data-lucide="search-x" class="w-12 h-12"></i>
            </div>
            <div class="space-y-3">
                <h3 class="text-2xl font-black text-main">{{ __('لا توجد نتائج!') }}</h3>
                <p class="text-muted/40 text-xs max-w-sm mx-auto leading-relaxed">{{ __('لم يتم العثور على أي بيانات مطابقة لاستفسارك. حاول استخدام كلمات مفتاحية أخرى.') }}</p>
            </div>
        </div>
    @endif
</div>
@endsection
