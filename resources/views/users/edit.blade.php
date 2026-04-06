@extends('layouts.admin')

@section('title', __('تعديل المستخدم'))

@section('content')
<div class="space-y-12 pb-20" dir="rtl">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">
        <div>
            <h2 class="text-4xl font-black text-main uppercase tracking-widest mb-4">{{ __('تعديل بيانات الحساب') }}</h2>
            <div class="h-1.5 w-24 bg-gold shadow-[0_0_15px_rgba(212,175,55,0.4)] rounded-full"></div>
        </div>
        
        <div class="flex flex-wrap items-center gap-4">
            <a href="{{ route('users.index') }}" class="px-8 py-4 bg-card border border-main text-muted/40 rounded-xl text-[10px] font-black uppercase tracking-widest flex items-center gap-3 hover:text-gold transition-all">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                {{ __('العودة للقائمة') }}
            </a>
        </div>
    </div>

    @if($errors->any())
    <div class="p-6 bg-rose-500/10 border border-rose-500/20 text-rose-500 rounded-3xl font-black text-sm text-center">
        @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
    @endif

    <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8" x-data="{ role: '{{ $user->role }}' }">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Sidebar: Identity -->
            <div class="lg:col-span-1 space-y-8">
                <div class="luxury-card p-10 border border-main/10 bg-main/5 space-y-8 text-center">
                    <div class="relative w-32 h-32 mx-auto group">
                        <div class="w-full h-full rounded-full bg-gold/5 border-2 border-dashed border-gold/20 flex items-center justify-center text-3xl font-black text-gold mx-auto shadow-2xl overflow-hidden relative">
                            <img id="avatar-preview" src="{{ $user->profile_image_url }}" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                                <i data-lucide="camera" class="w-8 h-8 text-white"></i>
                            </div>
                        </div>
                        <input type="file" name="profile_image" class="absolute inset-0 opacity-0 cursor-pointer" onchange="document.getElementById('avatar-preview').src = window.URL.createObjectURL(this.files[0])">
                    </div>

                    <div>
                        <h4 class="text-xl font-black text-main tracking-widest uppercase">{{ $user->name }}</h4>
                        <p class="text-[10px] text-muted/40 font-black uppercase tracking-widest mt-2">{{ __($user->role) }}</p>
                    </div>
                    
                    <div class="space-y-4 pt-6 border-t border-main/10">
                        <div class="space-y-2">
                             <label class="text-[10px] text-muted/40 font-black uppercase tracking-widest mr-1 block text-right">{{ __('تغيير الدور') }}</label>
                             <select name="role" x-model="role" class="input-luxury w-full py-4 text-sm font-bold">
                                <option value="customer" {{ $user->role == 'customer' ? 'selected' : '' }}>{{ __('عميل') }}</option>
                                <option value="merchant" {{ $user->role == 'merchant' ? 'selected' : '' }}>{{ __('تاجر') }}</option>
                                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>{{ __('مدير نظام') }}</option>
                                <option value="moderator" {{ $user->role == 'moderator' ? 'selected' : '' }}>{{ __('مشرف محتوى') }}</option>
                                <option value="support" {{ $user->role == 'support' ? 'selected' : '' }}>{{ __('دعم فني') }}</option>
                             </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main: User Info & Permissions -->
            <div class="lg:col-span-2 space-y-8">
                <div class="luxury-card p-10 border border-main/10">
                    <h4 class="text-lg font-black text-main mb-8 uppercase tracking-widest flex items-center gap-3">
                        <i data-lucide="info" class="w-5 h-5 text-gold"></i>
                        {{ __('البيانات الأساسية') }}
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-3">
                            <label class="text-[10px] text-muted/40 font-black uppercase tracking-widest mr-1">{{ __('الاسم الكامل') }}</label>
                            <input type="text" name="name" value="{{ $user->name }}" class="input-luxury w-full py-4 text-sm" placeholder="{{ __('أدخل اسم المستخدم...') }}" required>
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] text-muted/40 font-black uppercase tracking-widest mr-1">{{ __('البريد الإلكتروني') }}</label>
                            <input type="email" name="email" value="{{ $user->email }}" class="input-luxury w-full py-4 text-sm" placeholder="email@example.com" required>
                        </div>
                        <div class="space-y-3">
                            <label class="text-[10px] text-muted/40 font-black uppercase tracking-widest mr-1">{{ __('رقم الهاتف') }}</label>
                            <input type="text" name="phone" value="{{ $user->phone }}" class="input-luxury w-full py-4 text-sm" placeholder="+967..." required>
                        </div>
                    </div>
                    
                    <div class="pt-10 border-t border-main/5 mt-10 space-y-8" x-data="{ showPasswords: false }">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-sm font-black text-main uppercase tracking-widest flex items-center gap-3">
                                    <i data-lucide="lock" class="w-4 h-4 text-gold"></i>
                                    {{ __('تغيير كلمة المرور') }} <span class="text-[8px] text-muted/40 font-medium">({{ __('اختياري') }})</span>
                                </h4>
                                <button type="button" @click="showPasswords = !showPasswords" class="text-[10px] font-black text-gold uppercase tracking-widest hover:underline flex items-center gap-2">
                                    <!-- Eye Open Icon -->
                                    <svg x-show="!showPasswords" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></svg>
                                    <!-- Eye Closed Icon -->
                                    <svg x-show="showPasswords" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye-off"><path d="M10.733 5.076a10.744 10.744 0 0 1 11.205 6.575 1 1 0 0 1 0 .696 10.747 10.747 0 0 1-1.444 2.49"/><path d="M14.084 14.158a3 3 0 0 1-4.242-4.242"/><path d="M17.479 17.499a10.75 10.75 0 0 1-15.417-5.151 1 1 0 0 1 0-.696 10.75 10.75 0 0 1 4.446-5.143"/><line x1="2" x2="22" y1="2" y2="22"/></svg>
                                    <span x-text="showPasswords ? '{{ __('إخفاء') }}' : '{{ __('إظهار') }}'"></span>
                                </button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-3">
                                    <label class="text-[10px] text-muted/40 font-black uppercase tracking-widest mr-1">{{ __('كلمة المرور الجديدة') }}</label>
                                    <input :type="showPasswords ? 'text' : 'password'" name="password" value="{{ $user->password_plain }}" class="input-luxury w-full py-4 text-sm" placeholder="{{ __('اتركه فارغاً للحفاظ على الحالية') }}">
                                </div>
                                <div class="space-y-3">
                                    <label class="text-[10px] text-muted/40 font-black uppercase tracking-widest mr-1">{{ __('تأكيد كلمة المرور الجديدة') }}</label>
                                    <input :type="showPasswords ? 'text' : 'password'" name="password_confirmation" value="{{ $user->password_plain }}" class="input-luxury w-full py-4 text-sm" placeholder="{{ __('تأكيد كلمة المرور الجديدة') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Permissions Matrix (Conditional) -->
                <div x-show="['admin', 'moderator', 'support'].includes(role)" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform translate-y-4"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     class="luxury-card p-10 border border-gold/20 bg-gold/5">
                    <h4 class="text-xl font-black text-gold mb-8 uppercase tracking-widest flex items-center gap-4">
                        <i data-lucide="shield-check" class="w-6 h-6"></i>
                        {{ __('تعديل صلاحيات النظام') }}
                    </h4>

                    @php
                        $screens = [
                            'dashboard' => 'الرئيسية',
                            'merchants' => 'التجار',
                            'products' => 'المنتجات',
                            'bookings' => 'الحجوزات',
                            'custom_designs' => 'طلبات التصميم',
                            'categories' => 'الأقسام',
                            'users' => 'المستخدمين',
                            'currencies' => 'العملات',
                            'banners' => 'الإعلانات',
                            'notifications' => 'الإشعارات',
                            'gold_prices' => 'أسعار الذهب',
                            'contacts' => 'التواصل',
                            'settings' => 'الإعدادات',
                            'reports' => 'التقارير'
                        ];
                    @endphp

                    <div class="overflow-x-auto">
                        <table class="w-full text-right border-collapse">
                            <thead>
                                <tr class="text-[10px] font-black uppercase text-muted/40 border-b border-gold/10">
                                    <th class="py-4 pr-4">{{ __('الواجهة') }}</th>
                                    <th class="py-4 text-center">{{ __('فتح') }}</th>
                                    <th class="py-4 text-center">{{ __('إضافة') }}</th>
                                    <th class="py-4 text-center">{{ __('تعديل') }}</th>
                                    <th class="py-4 text-center">{{ __('حذف') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gold/5">
                                @foreach($screens as $key => $label)
                                    <tr class="group hover:bg-gold/5 transition-colors">
                                        <td class="py-4 pr-4 text-sm font-black text-main">{{ $label }}</td>
                                        <td class="py-4 text-center"><input type="checkbox" name="permissions[{{ $key }}][view]" {{ $user->hasPermission($key, 'view') ? 'checked' : '' }} class="w-4 h-4 rounded border-gold/20 text-gold focus:ring-gold/20"></td>
                                        <td class="py-4 text-center"><input type="checkbox" name="permissions[{{ $key }}][create]" {{ $user->hasPermission($key, 'create') ? 'checked' : '' }} class="w-4 h-4 rounded border-gold/20 text-gold focus:ring-gold/20"></td>
                                        <td class="py-4 text-center"><input type="checkbox" name="permissions[{{ $key }}][edit]" {{ $user->hasPermission($key, 'edit') ? 'checked' : '' }} class="w-4 h-4 rounded border-gold/20 text-gold focus:ring-gold/20"></td>
                                        <td class="py-4 text-center"><input type="checkbox" name="permissions[{{ $key }}][delete]" {{ $user->hasPermission($key, 'delete') ? 'checked' : '' }} class="w-4 h-4 rounded border-gold/20 text-gold focus:ring-gold/20"></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="px-16 py-5 bg-gold text-onyx rounded-2xl text-xs font-black uppercase tracking-widest shadow-2xl shadow-gold/30 hover:scale-105 active:scale-95 transition-all">
                        {{ __('حفظ التعديلات') }}
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
