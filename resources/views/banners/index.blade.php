@extends('layouts.admin')

@section('title', __('إدارة الإعلانات'))

@section('content')
<div class="space-y-12 pb-20" dir="rtl">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 pt-6">
        <div>
            <h2 class="text-4xl font-black text-main uppercase tracking-widest mb-4">{{ __('إعلانات الواجهة') }}</h2>
            <div class="h-1.5 w-24 bg-gold shadow-[0_0_15px_rgba(212,175,55,0.4)] rounded-full"></div>
        </div>
        
        <div class="flex flex-wrap items-center gap-4">
            <a href="{{ route('banners.create') }}" class="px-10 py-4 bg-gold text-onyx rounded-xl text-[10px] font-black uppercase tracking-widest shadow-xl shadow-gold/20 hover:scale-105 transition-all flex items-center gap-3 text-center">
                <i data-lucide="plus" class="w-4 h-4"></i>
                {{ __('إضافة إعلان جديد') }}
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="p-6 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 rounded-3xl font-black text-sm text-center">
        {{ session('success') }}
    </div>
    @endif

    <!-- Banners Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
        @forelse($banners as $banner)
            <div class="luxury-card p-0 overflow-hidden group border border-main/10 hover:border-gold/30 transition-all duration-500 relative flex flex-col h-full bg-main/5">
                <!-- Media Preview -->
                <div class="relative h-56 bg-main/10 overflow-hidden group/media">
                    @if($banner->type == 'video')
                        <div class="w-full h-full flex flex-col items-center justify-center bg-onyx text-gold space-y-3">
                            <i data-lucide="play-circle" class="w-16 h-16 opacity-40"></i>
                            <span class="text-[8px] font-black uppercase tracking-widest">{{ __('إعلان فيديو') }}</span>
                        </div>
                    @elseif($banner->image_url)
                        <img src="{{ $banner->image_url }}" class="w-full h-full object-cover group-hover/media:scale-110 transition-transform duration-1000">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gold/5 text-gold/10">
                            <i data-lucide="image" class="w-16 h-16"></i>
                        </div>
                    @endif
                    
                    <!-- Status Badge (FIXED: using is_active) -->
                    <div class="absolute top-4 right-4 px-4 py-1.5 bg-{{ $banner->is_active ? 'emerald' : 'rose' }}-500/20 border border-{{ $banner->is_active ? 'emerald' : 'rose' }}-500/30 backdrop-blur-md rounded-full text-[9px] font-black text-{{ $banner->is_active ? 'emerald' : 'rose' }}-500 uppercase tracking-widest shadow-lg">
                        {{ $banner->is_active ? __('نشط') : __('متوقف') }}
                    </div>

                    <!-- Placement Badge -->
                    <div class="absolute bottom-4 right-4 px-3 py-1 bg-black/60 border border-gold/20 backdrop-blur-sm rounded-lg text-[7px] font-black text-white uppercase tracking-widest">
                        {{ __($banner->placement) }}
                    </div>
                </div>

                <!-- Content & Stats -->
                <div class="p-8 space-y-6 flex-1 flex flex-col justify-between">
                    <div>
                        <h4 class="text-xl font-black text-main mb-2 tracking-tight line-clamp-1 truncate">{{ $banner->title }}</h4>
                        <div class="flex items-center gap-3">
                             <span class="px-2 py-0.5 bg-main/10 rounded-md text-[7px] font-black text-muted/40 uppercase tracking-widest">{{ $banner->type == 'video' ? __('فيديو') : __('صورة') }}</span>
                             <span class="px-2 py-0.5 bg-gold/10 rounded-md text-[7px] font-black text-gold uppercase tracking-widest">{{ __('الجمهور') }}: {{ __($banner->target) }}</span>
                        </div>
                    </div>

                    <div class="p-3 bg-white/5 rounded-2xl border border-main/5 space-y-2">
                        <p class="text-[8px] text-muted/40 font-black uppercase tracking-widest">{{ __('الرابط') }}</p>
                        <p class="text-[9px] text-main font-bold truncate">{{ $banner->link ?: ($banner->video_url ?: __('لا يوجد')) }}</p>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-3 pt-4 border-t border-main/5">
                        <form action="{{ route('banners.toggle', $banner->id) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full py-3 bg-{{ $banner->is_active ? 'rose' : 'emerald' }}-500/10 border border-{{ $banner->is_active ? 'rose' : 'emerald' }}-500/20 rounded-xl text-{{ $banner->is_active ? 'rose' : 'emerald' }}-500 text-[9px] font-black uppercase tracking-widest hover:scale-105 transition-all">
                                {{ $banner->is_active ? __('إيقاف') : __('تفعيل') }}
                            </button>
                        </form>
                        
                        <a href="{{ route('banners.edit', $banner->id) }}" class="p-3 bg-card border border-main rounded-xl text-muted/40 hover:text-gold transition-all" title="{{ __('تعديل') }}">
                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                        </a>
                        
                        <form action="{{ route('banners.destroy', $banner->id) }}" method="POST" onsubmit="return confirm('{{ __('حذف الإعلان؟') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-3 bg-card border border-main rounded-xl text-muted/40 hover:text-rose-500 transition-all">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full luxury-card p-24 text-center space-y-8 border border-dashed border-main/20 bg-main/5">
                <div class="w-24 h-24 bg-gold/5 rounded-full flex items-center justify-center mx-auto text-gold/10 border border-gold/5 animate-pulse">
                    <i data-lucide="image" class="w-12 h-12"></i>
                </div>
                <div class="space-y-3">
                    <h3 class="text-2xl font-black text-main">{{ __('لا توجد إعلانات!') }}</h3>
                    <p class="text-muted/40 text-xs max-w-sm mx-auto leading-relaxed">{{ __('ابدأ بإضافة أول إعلان للظهور في واجهة التطبيق الرئيسية.') }}</p>
                </div>
                <div class="flex justify-center gap-4">
                    <a href="{{ route('banners.create') }}" class="px-10 py-4 bg-gold text-onyx rounded-xl text-[10px] font-black uppercase tracking-widest shadow-xl shadow-gold/20 hover:scale-105 transition-all">{{ __('إضافة أول إعلان') }}</a>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
