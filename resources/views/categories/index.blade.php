@extends('layouts.admin')

@section('title', __('إدارة الأقسام'))

@section('content')
<div class="space-y-12 pb-20" dir="rtl">
    <!-- Header: Premium Alignment -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-10 pt-8 border-b border-main/5 pb-10">
        <div class="relative">
            <h2 class="text-5xl font-black text-main uppercase tracking-[0.2em] leading-tight">{{ __('أقسام المنتجات') }}</h2>
            <div class="flex items-center gap-3 mt-4">
                <div class="h-1.5 w-20 bg-gold shadow-[0_0_20px_rgba(212,175,55,0.6)] rounded-full"></div>
                <div class="h-1.5 w-6 bg-gold/30 rounded-full"></div>
                <p class="text-[10px] text-muted/60 font-black uppercase tracking-[0.3em] mr-2">{{ __('بناء الهيكل') }}</p>
            </div>
        </div>
        
        <div class="flex flex-wrap items-center gap-5">
            <form action="{{ route('categories.clear') }}" method="POST" onsubmit="return confirm('{{ __('تحذير: هذا سيقوم بمسح جميع الأقسام والارتباطات! هل أنت متأكد؟') }}')">
                @csrf
                <button type="submit" class="px-8 py-4 bg-rose-500/5 border border-rose-500/10 text-rose-500 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-rose-500 hover:text-white transition-all duration-500">{{ __('مسح الكل') }}</button>
            </form>
            
            
            <a href="{{ route('categories.create') }}" class="px-12 py-5 bg-gradient-to-tr from-gold to-[#E8D095] text-onyx rounded-2xl text-[10px] font-black uppercase tracking-[0.1em] shadow-[0_15px_30px_rgba(212,175,55,0.25)] hover:scale-105 hover:shadow-[0_20px_40px_rgba(212,175,55,0.35)] active:scale-95 transition-all duration-500 flex items-center gap-4">
                <i data-lucide="plus" class="w-4 h-4"></i>
                {{ __('إضافة فئة ملكية') }}
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="p-6 bg-emerald-500/5 border border-emerald-500/10 text-emerald-500 rounded-3xl font-black text-xs text-center animate-in fade-in slide-in-from-top-4 duration-700">
        {{ session('success') }}
    </div>
    @endif

    <!-- Categories Grid (3 Columns) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
        @forelse($categories as $category)
            <div class="group relative bg-white dark:bg-onyx/20 rounded-[2.5rem] border border-main/10 hover:border-gold/40 transition-all duration-700 shadow-sm hover:shadow-[0_40px_80px_rgba(0,0,0,0.15)] flex flex-col h-full overflow-hidden">
                
                <!-- Card Inner Glow Overlay -->
                <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-gold/[0.02] opacity-0 group-hover:opacity-100 transition-opacity duration-700 pointer-events-none"></div>

                <!-- Media Section -->
                <div class="relative h-60 overflow-hidden m-4 rounded-[2rem] bg-main/5">
                    @if($category->image)
                        <img src="{{ $category->image_url }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000 ease-out">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gold/5 text-gold/20">
                            <i data-lucide="layout-grid" class="w-20 h-20 opacity-30"></i>
                        </div>
                    @endif
                    
                    <!-- Floating Brand/Order Overlay -->
                    <div class="absolute top-6 right-6 backdrop-blur-xl bg-black/40 border border-white/10 px-5 py-2.5 rounded-2xl shadow-2xl">
                        <span class="text-[8px] font-black text-gold/60 uppercase tracking-widest block mb-0.5 text-center">{{ __('ترتيب') }}</span>
                        <span class="text-xl font-bold text-white text-center block">#{{ $category->display_order }}</span>
                    </div>

                    <!-- Quick Actions Overlay -->
                    <div class="absolute inset-0 bg-onyx/60 backdrop-blur-md opacity-0 group-hover:opacity-100 transition-all duration-700 flex items-center justify-center gap-6">
                        <a href="{{ route('categories.edit', $category->id) }}" class="w-14 h-14 bg-white text-onyx rounded-2xl flex items-center justify-center shadow-2xl hover:bg-gold hover:scale-110 transition-all duration-500">
                            <i data-lucide="edit-3" class="w-6 h-6"></i>
                        </a>
                        <form action="{{ route('categories.destroy', $category->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-14 h-14 bg-rose-500/80 backdrop-blur-md text-white rounded-2xl flex items-center justify-center shadow-2xl hover:bg-rose-600 hover:scale-110 transition-all duration-500" onclick="return confirm('{{ __('تأكيد حذف الفئة؟') }}')">
                                <i data-lucide="trash-2" class="w-6 h-6"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Info Content -->
                <div class="px-10 pb-10 pt-4 flex-1 flex flex-col justify-between">
                    <div>
                        <h4 class="text-3xl font-black text-main text-center tracking-tight mb-6 group-hover:text-gold transition-colors duration-500">{{ $category->name }}</h4>
                        <div class="h-1 w-12 bg-gold/20 mx-auto rounded-full group-hover:w-24 transition-all duration-500"></div>
                    </div>

                    <!-- Enhanced Performance Stats -->
                    <div class="grid grid-cols-2 gap-4 mt-10">
                        <!-- Total -->
                        <div class="relative p-5 rounded-[1.5rem] bg-main/5 border border-main/5 group/stat hover:bg-white dark:hover:bg-white/5 hover:border-gold/20 transition-all duration-500 overflow-hidden">
                            <div class="absolute -top-4 -left-4 w-12 h-12 bg-gold/5 rounded-full blur-xl group-hover/stat:bg-gold/20 transition-all"></div>
                            <p class="text-[8px] font-black text-muted/40 uppercase tracking-[0.2em] mb-3">{{ __('إجمالي القطع') }}</p>
                            <span class="text-2xl font-black text-main leading-none">{{ $category->total_count }}</span>
                        </div>
                        
                        <!-- Gold -->
                        <div class="p-5 rounded-[1.5rem] bg-gold/5 border border-gold/10 flex flex-col justify-between hover:bg-gold/10 transition-all duration-500">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-[8px] font-black text-gold uppercase tracking-[0.2em]">{{ __('الذهب') }}</span>
                                <i data-lucide="gem" class="w-3 h-3 text-gold"></i>
                            </div>
                            <span class="text-2xl font-black text-gold leading-none">{{ $category->gold_count }}</span>
                        </div>

                        <!-- Silver -->
                        <div class="p-5 rounded-[1.5rem] bg-slate-500/5 border border-slate-500/10 flex flex-col justify-between hover:bg-slate-500/10 transition-all duration-500">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-[8px] font-black text-slate-400 uppercase tracking-[0.2em]">{{ __('الفضة') }}</span>
                                <i data-lucide="circle-dot" class="w-3 h-3 text-slate-400"></i>
                            </div>
                            <span class="text-2xl font-black text-main leading-none">{{ $category->silver_count }}</span>
                        </div>

                        <!-- Stones -->
                        <div class="p-5 rounded-[1.5rem] bg-emerald-500/5 border border-emerald-500/10 flex flex-col justify-between hover:bg-emerald-500/10 transition-all duration-500">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-[8px] font-black text-emerald-500 uppercase tracking-[0.2em]">{{ __('الأحجار') }}</span>
                                <i data-lucide="sparkles" class="w-3 h-3 text-emerald-500"></i>
                            </div>
                            <span class="text-2xl font-black text-main leading-none">{{ $category->stone_count }}</span>
                        </div>
                    </div>
                </div>

                <!-- Hidden Deep Link -->
                <a href="{{ route('products.index', ['category_id' => $category->id]) }}" class="py-5 bg-main/5 hover:bg-gold hover:text-onyx text-center border-t border-main/10 transition-all duration-500 group/link">
                   <span class="text-[9px] font-black uppercase tracking-[0.25em] flex items-center justify-center gap-3">
                       {{ __('استعراض المخزون الملكي') }}
                       <i data-lucide="arrow-left" class="w-3 h-3 group-hover/link:-translate-x-2 transition-transform"></i>
                   </span>
                </a>
            </div>
        @empty
            <div class="lg:col-span-3 pb-20">
                <div class="luxury-card p-32 text-center space-y-10 border border-dashed border-main/20 bg-main/5 relative overflow-hidden">
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,rgba(212,175,55,0.05),transparent)]"></div>
                    <div class="w-32 h-32 bg-gold/5 rounded-full flex items-center justify-center mx-auto text-gold/10 border border-gold/10 relative">
                        <i data-lucide="folder-search" class="w-16 h-16 opacity-20"></i>
                    </div>
                    <div class="space-y-4">
                        <h3 class="text-3xl font-black text-main uppercase tracking-widest">{{ __('لا توجد فئات حالياً') }}</h3>
                        <p class="text-muted/40 text-xs max-w-sm mx-auto leading-relaxed">{{ __('ابدأ ببناء هيكل تصنيف منتجاتك الفاخرة لتنظيم المتجر بشكل ملكي.') }}</p>
                    </div>
                    <div class="flex justify-center pt-4">
                        <a href="{{ route('categories.create') }}" class="px-12 py-5 bg-gold text-onyx rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-2xl shadow-gold/30 hover:scale-105 transition-all">{{ __('تأسيس أول فئة') }}</a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
