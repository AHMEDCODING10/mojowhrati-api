@extends('layouts.admin')

@section('title', __('إدارة المستخدمين'))

@section('content')
<div class="space-y-12 pb-20" dir="rtl">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">
        <div>
            <h2 class="text-4xl font-black text-main uppercase tracking-widest mb-4">{{ __('المستخدمين') }}</h2>
            <div class="h-1.5 w-24 bg-gold shadow-[0_0_15px_rgba(212,175,55,0.4)] rounded-full"></div>
        </div>
        
        <div class="flex flex-wrap items-center gap-4">
            <a href="{{ route('users.create') }}" class="px-10 py-4 bg-gold text-onyx rounded-xl text-[10px] font-black uppercase tracking-widest shadow-xl shadow-gold/20 hover:scale-105 transition-all flex items-center gap-3 text-center">
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                {{ __('إضافة مستخدم جديد') }}
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="px-8 py-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 rounded-2xl font-black text-sm text-center shadow-lg shadow-emerald-500/5 animate-in fade-in slide-in-from-top-4 duration-700">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="px-8 py-4 bg-rose-500/10 border border-rose-500/20 text-rose-500 rounded-2xl font-black text-sm text-center shadow-lg shadow-rose-500/5 animate-in fade-in slide-in-from-top-4 duration-700">
        {{ session('error') }}
    </div>
    @endif

    @if($errors->any())
    <div class="px-8 py-4 bg-rose-500/10 border border-rose-500/20 text-rose-500 rounded-2xl font-black text-sm text-center shadow-lg shadow-rose-500/5 animate-in fade-in slide-in-from-top-4 duration-700">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Search & Filters -->
    <div class="luxury-card p-8 border border-gold/10 bg-white/50 dark:bg-[#1A1A1A]/50 backdrop-blur-md rounded-[2.5rem] shadow-2xl shadow-gold/5">
        <form action="{{ route('users.index') }}" method="GET" class="flex flex-col lg:flex-row gap-8 items-center justify-between">
            <!-- Search Input -->
            <div class="relative w-full lg:max-w-md group">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('ابحث باسم المستخدم، البريد، أو الهاتف...') }}" 
                       class="w-full bg-white dark:bg-black/40 border border-gold/10 rounded-2xl px-14 py-4 text-sm text-main focus:ring-2 focus:ring-gold/20 focus:border-gold outline-none transition-all placeholder:text-muted/30">
                <i data-lucide="search" class="absolute left-5 top-1/2 -translate-y-1/2 w-5 h-5 text-gold/40 group-focus-within:text-gold transition-colors"></i>
            </div>

            <!-- Role Tabs -->
            <div class="flex items-center gap-2 p-1.5 bg-black/5 dark:bg-white/5 rounded-2xl border border-gold/5 overflow-x-auto max-w-full">
                <a href="{{ route('users.index', array_merge(request()->query(), ['role' => null])) }}" 
                   class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ !request('role') ? 'bg-gold text-onyx shadow-lg shadow-gold/20' : 'text-muted/40 hover:text-gold' }}">
                    {{ __('الكل') }}
                </a>
                <a href="{{ route('users.index', array_merge(request()->query(), ['role' => 'merchant'])) }}" 
                   class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ request('role') == 'merchant' ? 'bg-indigo-500 text-white shadow-lg shadow-indigo-500/20' : 'text-muted/40 hover:text-indigo-500' }}">
                    {{ __('التجار') }}
                </a>
                <a href="{{ route('users.index', array_merge(request()->query(), ['role' => 'customer'])) }}" 
                   class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ request('role') == 'customer' ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/20' : 'text-muted/40 hover:text-emerald-500' }}">
                    {{ __('العملاء') }}
                </a>
                <a href="{{ route('users.index', array_merge(request()->query(), ['role' => 'admin'])) }}" 
                   class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all {{ in_array(request('role'), ['admin', 'moderator', 'support', 'super_admin']) ? 'bg-onyx text-gold shadow-lg shadow-onyx/20' : 'text-muted/40 hover:text-onyx dark:hover:text-white' }}">
                    {{ __('الإدارة') }}
                </a>
            </div>

            <!-- Status Toggle -->
            <div class="flex items-center gap-4 bg-gold/5 border border-gold/10 p-1.5 rounded-2xl">
                <a href="{{ route('users.index', array_merge(request()->query(), ['status' => null])) }}" 
                   class="px-5 py-2 rounded-xl text-[9px] font-black uppercase transition-all {{ !request('status') ? 'bg-gold/20 text-gold shadow-sm' : 'text-muted/30 hover:text-gold' }}">
                    {{ __('الحالة: الكل') }}
                </a>
                <a href="{{ route('users.index', array_merge(request()->query(), ['status' => 'active'])) }}" 
                   class="px-5 py-2 rounded-xl text-[9px] font-black uppercase transition-all {{ request('status') == 'active' ? 'bg-emerald-500/10 text-emerald-500' : 'text-muted/30 hover:text-emerald-500' }}">
                    {{ __('نشط') }}
                </a>
                <a href="{{ route('users.index', array_merge(request()->query(), ['status' => 'blocked'])) }}" 
                   class="px-5 py-2 rounded-xl text-[9px] font-black uppercase transition-all {{ request('status') == 'blocked' ? 'bg-rose-500/10 text-rose-500' : 'text-muted/30 hover:text-rose-500' }}">
                    {{ __('موقوف') }}
                </a>
            </div>
            
            <button type="submit" class="hidden"></button>
        </form>
    </div>

    <!-- Users Table -->
    <div class="luxury-card overflow-hidden border border-gold/10 bg-white/50 dark:bg-[#1A1A1A]/50 backdrop-blur-md rounded-[3rem] shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-right border-collapse">
                <thead>
                    <tr class="header-bg text-onyx dark:text-gold text-[11px] font-black uppercase tracking-[0.2em]">
                        <th class="px-10 py-8 border-b border-white/20">{{ __('المستخدم') }}</th>
                        <th class="px-10 py-8 border-b border-white/20 text-center">{{ __('الدور') }}</th>
                        <th class="px-10 py-8 border-b border-white/20 text-center">{{ __('الحالة') }}</th>
                        <th class="px-10 py-8 border-b border-white/20">{{ __('تاريخ الانضمام') }}</th>
                        <th class="px-10 py-8 border-b border-white/20 text-center">{{ __('الإجراءات') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gold/5">
                    @forelse($users as $user)
                        <tr class="group hover:bg-gold/5 transition-all duration-500">
                            <td class="px-10 py-8 text-main font-bold">
                                <div class="flex items-center gap-6">
                                    <div class="w-14 h-14 rounded-2xl bg-white dark:bg-black border border-gold/20 flex items-center justify-center text-gold font-black uppercase group-hover:scale-110 group-hover:rotate-6 transition-all shadow-lg overflow-hidden relative">
                                        <img src="{{ $user->profile_image_url }}" class="w-full h-full object-cover">
                                        <div class="absolute inset-x-0 bottom-0 h-1 bg-gold shadow-[0_0_10px_rgba(212,175,55,0.5)]"></div>
                                    </div>
                                    <div>
                                        <div class="text-lg font-black tracking-tight text-main">{{ $user->name }}</div>
                                        <div class="text-[10px] text-muted/40 font-black uppercase tracking-widest mt-1 flex items-center gap-2">
                                            <i data-lucide="{{ $user->phone ? 'phone' : 'mail' }}" class="w-3 h-3 text-gold/60"></i>
                                            {{ $user->phone ?? $user->email }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-10 py-8 text-center text-sm font-black text-main">
                                <div class="flex flex-col items-center gap-2">
                                    <span class="px-4 py-1.5 bg-gold/10 border border-gold/20 text-gold text-[9px] font-black uppercase tracking-widest rounded-full shadow-sm">
                                        {{ __($user->role) }}
                                    </span>
                                    <div class="flex gap-1">
                                        @if($user->canAdd()) <div class="w-1.5 h-1.5 rounded-full bg-emerald-500" title="إضافة"></div> @endif
                                        @if($user->canEdit()) <div class="w-1.5 h-1.5 rounded-full bg-indigo-500" title="تعديل"></div> @endif
                                        @if($user->canDelete()) <div class="w-1.5 h-1.5 rounded-full bg-rose-500" title="حذف"></div> @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-10 py-8 text-center">
                                @if($user->status === 'active')
                                    <span class="px-5 py-2.5 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl inline-flex items-center gap-3 shadow-md">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse shadow-[0_0_10px_rgba(16,185,129,0.5)]"></span>
                                        {{ __('نشط') }}
                                    </span>
                                @else
                                    <span class="px-5 py-2.5 bg-rose-500/10 border border-rose-500/20 text-rose-500 text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl inline-flex items-center gap-3 shadow-md italic">
                                        <span class="w-2 h-2 rounded-full bg-rose-500 shadow-[0_0_10px_rgba(244,63,94,0.5)]"></span>
                                        {{ __('موقوف') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-10 py-8">
                                <div class="flex flex-col">
                                    <span class="text-main font-black tracking-tight">{{ optional($user->created_at)->format('Y/m/d') ?? '-' }}</span>
                                    <span class="text-[9px] text-muted/30 font-black uppercase tracking-widest mt-1">{{ optional($user->created_at)->diffForHumans() ?? '' }}</span>
                                </div>
                            </td>
                            <td class="px-10 py-8">
                                <div class="flex items-center justify-center gap-4">
                                    <!-- Toggle Status Button -->
                                    <form action="{{ route('users.toggle-status', $user->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-12 h-12 bg-white dark:bg-black border border-gold/10 rounded-2xl {{ $user->status === 'active' ? 'text-amber-500 hover:text-rose-500 hover:bg-rose-500/10' : 'text-muted/40 hover:text-emerald-500 hover:bg-emerald-500/10' }} transition-all flex items-center justify-center shadow-sm" 
                                                title="{{ $user->status === 'active' ? __('إيقاف الحساب') : __('تفعيل الحساب') }}">
                                            <i data-lucide="{{ $user->status === 'active' ? 'shield-off' : 'shield-check' }}" class="w-5 h-5"></i>
                                        </button>
                                    </form>

                                    @if(auth()->user()->canEdit())
                                        <a href="{{ route('users.edit', $user->id) }}" class="w-12 h-12 bg-white dark:bg-black border border-gold/10 rounded-2xl text-muted/40 hover:text-gold hover:border-gold transition-all flex items-center justify-center shadow-sm" title="{{ __('تعديل البيانات') }}">
                                            <i data-lucide="edit" class="w-5 h-5"></i>
                                        </a>
                                    @endif

                                    @if(auth()->user()->canDelete())
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('{{ __('هل أنت متأكد من حذف المستخدم نهائياً؟') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-12 h-12 bg-rose-500/5 border border-rose-500/10 rounded-2xl text-rose-500/40 hover:bg-rose-500 hover:text-white transition-all flex items-center justify-center shadow-sm">
                                                <i data-lucide="trash-2" class="w-5 h-5"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-10 py-32 text-center">
                                <div class="w-32 h-32 bg-gold/5 rounded-full flex items-center justify-center mx-auto text-gold/20 mb-8 border border-gold/5 animate-pulse">
                                    <i data-lucide="users" class="w-16 h-16"></i>
                                </div>
                                <h3 class="text-3xl font-black text-main tracking-tight mb-3 italic opacity-40">{{ __('لا يوجد مستخدمين لهذه الفئة') }}</h3>
                                <p class="text-muted/40 text-[10px] font-black uppercase tracking-widest">{{ __('جرب استخدام كلمات بحث أخرى أو تغيير الفلاتر المحددة.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="p-8 border-t border-main/10 bg-main/5 text-right">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
