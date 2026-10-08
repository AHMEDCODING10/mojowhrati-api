@extends('layouts.admin')

@section('title', __('إدارة الإعلانات والتحديثات'))

@section('content')
<div class="space-y-12 pb-20" dir="rtl">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 pt-6">
        <div>
            <h2 class="text-4xl font-black text-main uppercase tracking-widest mb-4">{{ __('إدارة الإعلانات والتحديثات') }}</h2>
            <div class="h-1.5 w-24 bg-gold shadow-[0_0_15px_rgba(212,175,55,0.4)] rounded-full"></div>
        </div>
        
        <div class="flex flex-wrap items-center gap-4">
            <a href="{{ route('banners.create') }}" class="px-8 py-4 bg-gold text-onyx rounded-xl text-[10px] font-black uppercase tracking-widest shadow-xl shadow-gold/20 hover:scale-105 transition-all flex items-center gap-3 text-center">
                <i data-lucide="plus" class="w-4 h-4"></i>
                {{ __('إضافة إعلان جديد') }}
            </a>
            
            <a href="{{ route('app-updates.create') }}" class="px-8 py-4 bg-gradient-to-tr from-slate-900 to-slate-800 text-gold border border-gold/30 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-xl hover:scale-105 hover:border-gold transition-all flex items-center gap-3 text-center">
                <i data-lucide="sparkles" class="w-4 h-4 text-gold animate-pulse"></i>
                {{ __('إضافة تحديث جديد') }}
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="p-6 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 rounded-3xl font-black text-sm text-center">
        {{ session('success') }}
    </div>
    @endif

    <!-- App Updates Section -->
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-gold/10 text-gold rounded-2xl">
                    <i data-lucide="smartphone" class="w-6 h-6"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-black text-main">{{ __('سجل تحديثات التطبيق (App Updates)') }}</h3>
                    <p class="text-xs text-muted/50 font-bold">{{ __('تحديثات وإصدارات التطبيق المرسلة للمستخدمين') }}</p>
                </div>
            </div>
            <span class="px-4 py-1.5 bg-main/5 border border-main/10 rounded-full text-[10px] font-black text-gold uppercase tracking-widest">
                {{ count($appUpdates ?? []) }} {{ __('تحديثات صادرة') }}
            </span>
        </div>

        @if(isset($appUpdates) && count($appUpdates) > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($appUpdates as $update)
            <div class="luxury-card p-6 border border-main/10 bg-main/5 hover:border-gold/30 transition-all rounded-3xl space-y-4">
                <div class="flex items-center justify-between pb-4 border-b border-main/10">
                    <div class="flex items-center gap-3">
                        <span class="px-4 py-2 bg-gradient-to-tr from-gold to-[#E8D095] text-black font-black text-sm rounded-2xl shadow-md">
                            {{ $update->version_number }}
                        </span>
                        <div>
                            <span class="px-3 py-1 bg-gold/10 text-gold text-[9px] font-black rounded-full">
                                {{ $update->target_audience == 'customer' ? 'العملاء' : ($update->target_audience == 'merchant' ? 'التجار' : 'الكل') }}
                            </span>
                            @if($update->is_force_update)
                            <span class="px-3 py-1 bg-rose-500/10 text-rose-500 text-[9px] font-black rounded-full mr-2">
                                تحديث إجباري
                            </span>
                            @endif
                        </div>
                    </div>
                    <form action="{{ route('app-updates.destroy', $update->id) }}" method="POST" onsubmit="return confirmDeleteForm(event, this, '{{ __('حذف التحديث') }}', '{{ __('هل أنت متأكد من حذف سجل التحديث هذا؟') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 text-muted/30 hover:text-rose-500 transition-colors" title="حذف">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>

                <div class="space-y-2">
                    <p class="text-[10px] text-muted/40 font-black uppercase tracking-widest">{{ __('الميزات الجديدة والتغييرات') }}</p>
                    <p class="text-sm font-bold text-main/80 whitespace-pre-line leading-relaxed">{{ $update->release_notes }}</p>
                </div>

                <div class="pt-3 border-t border-main/5 flex flex-wrap items-center justify-between text-[10px] font-bold text-muted/50 gap-2">
                    <span><i data-lucide="clock" class="w-3.5 h-3.5 inline ml-1"></i>{{ $update->created_at->format('Y-m-d H:i') }}</span>
                    <div class="flex items-center gap-3">
                        @if($update->apk_file_url)
                        <a href="{{ $update->download_url }}" target="_blank" class="text-gold hover:underline flex items-center gap-1">
                            <i data-lucide="download" class="w-3.5 h-3.5"></i> APK المنصة
                        </a>
                        @endif
                        @if($update->play_store_url)
                        <a href="{{ $update->play_store_url }}" target="_blank" class="text-emerald-500 hover:underline flex items-center gap-1">
                            <i data-lucide="external-link" class="w-3.5 h-3.5"></i> سوق بلاي
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="p-8 border border-dashed border-main/15 rounded-3xl bg-main/5 text-center text-muted/40 text-xs font-bold">
            {{ __('لا توجد تحديثات صادرة بعد. اضغط على "إضافة تحديث جديد" لإرسال أول تحديث للتطبيق.') }}
        </div>
        @endif
    </div>

    <!-- Banners Grid Section -->
    <div class="space-y-6 pt-6 border-t border-main/10">
        <h3 class="text-2xl font-black text-main">{{ __('إعلانات الواجهة (Banners)') }}</h3>

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
                        
                        <!-- Status Badge -->
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
                            
                            <form action="{{ route('banners.destroy', $banner->id) }}" method="POST" onsubmit="return confirmAction(this, '{{ __('هل أنت متأكد من حذف هذا الإعلان؟') }}', 'تأكيد حذف الإعلان')">
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
                <div class="col-span-full luxury-card p-16 text-center space-y-6 border border-dashed border-main/20 bg-main/5">
                    <div class="w-20 h-24 bg-gold/5 rounded-full flex items-center justify-center mx-auto text-gold/10 border border-gold/5">
                        <i data-lucide="image" class="w-10 h-10"></i>
                    </div>
                    <h3 class="text-xl font-black text-main">{{ __('لا توجد إعلانات حالياً!') }}</h3>
                    <a href="{{ route('banners.create') }}" class="px-8 py-3 bg-gold text-onyx rounded-xl text-[10px] font-black uppercase tracking-widest inline-block">{{ __('إضافة أول إعلان') }}</a>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
