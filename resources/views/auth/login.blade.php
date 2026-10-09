<x-guest-layout>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;900&display=swap');
        
        body {
            background-color: #fdfaf3; 
        }

        .premium-input {
            border: 1px solid #e5e5e5 !important;
            border-radius: 12px !important;
            padding: 1rem 1.25rem !important;
            font-weight: 600 !important;
            transition: all 0.3s ease !important;
        }

        .premium-input:focus {
            border-color: #D4AF37 !important;
            box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.1) !important;
            outline: none !important;
        }

        .gold-checkbox {
            appearance: none;
            width: 22px;
            height: 22px;
            border: 2px solid #D4AF37;
            border-radius: 50%;
            cursor: pointer;
            position: relative;
            transition: all 0.3s ease;
            margin-left: 8px;
        }

        .gold-checkbox:checked {
            background-color: #D4AF37;
        }

        .gold-checkbox:checked::after {
            content: '✓';
            color: white;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 13px;
            font-weight: bold;
        }

        .premium-btn {
            background: #D4AF37 !important;
            color: #111315 !important;
            border-radius: 40px !important;
            font-weight: 700 !important;
            padding: 1.25rem !important;
            box-shadow: 0 10px 20px rgba(212, 175, 55, 0.2) !important;
            transition: all 0.3s ease !important;
        }

        .premium-btn:hover {
            background: #B8860B !important;
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(212, 175, 55, 0.3) !important;
        }

        .gold-underline {
            display: inline-block;
            border-bottom: 4px solid #D4AF37;
            padding-bottom: 4px;
        }
    </style>

    <div class="w-full max-w-6xl px-4 flex flex-col items-center">
        <!-- Main Dual-Column Card -->
        <div class="bg-white overflow-hidden rounded-[40px] shadow-[0_20px_80px_rgba(0,0,0,0.06)] flex flex-col lg:flex-row min-h-[620px] w-full">
            
            <!-- RIGHT Column (Logo Section) - PLACED FIRST FOR RTL TO BE ON THE RIGHT -->
            <div class="w-full lg:w-1/2 p-12 flex flex-col items-center justify-center text-center relative bg-white lg:border-l border-gray-50 order-first">
                <div class="relative flex flex-col items-center">
                    <div class="relative mb-10 w-64 h-64 lg:w-80 lg:h-80 flex items-center justify-center">
                        <div class="absolute inset-0 border-[6px] border-[#D4AF37] rounded-full opacity-20 scale-110"></div>
                        <div class="absolute inset-0 border-2 border-dotted border-[#D4AF37] rounded-full scale-105"></div>
                        
                        <div class="bg-black rounded-full shadow-[0_30px_60px_rgba(0,0,0,0.35)] relative overflow-hidden group w-56 h-56 lg:w-72 lg:h-72 flex items-center justify-center">
                           <img src="/images/logo.jpg" alt="Logo" class="w-44 h-44 lg:w-56 lg:h-56 object-contain">
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <p class="text-gray-900 font-black text-xl lg:text-2xl tracking-tight leading-relaxed">
                            نظام الإدارة المتكامل لمتاجر المجوهرات
                        </p>
                        <p class="text-gray-400 text-xs font-black uppercase tracking-[0.4em]">
                            ELITE GOLD MANAGEMENT
                        </p>
                    </div>
                </div>
            </div>

            <!-- LEFT Column (Form Section) -->
            <div class="w-full lg:w-1/2 p-10 lg:p-20 flex flex-col justify-center order-last">
                <div class="max-w-md mx-auto w-full">
                    <!-- Title Section -->
                    <div class="mb-12 text-right">
                        <h2 class="text-4xl font-black text-gray-900 gold-underline" style="font-family: 'Montserrat', sans-serif;">
                            {{ __('تسجيل الدخول') }}
                        </h2>
                        <p class="text-gray-400 font-bold text-sm mt-4">
                            مرحباً بك مجدداً في نظام الإدارة النخبوية
                        </p>
                    </div>

                    <!-- Session Status -->
                    <x-auth-session-status class="mb-8" :status="session('status')" />

                    <form method="POST" action="{{ route('login') }}" class="space-y-8">
                        @csrf

                        <!-- Email Address -->
                        <!-- Username Address Field -->
                        <div class="space-y-3 text-right">
                            <div class="flex items-center justify-between">
                                <label for="email" class="block text-xs font-black text-gray-500 uppercase tracking-widest mr-1">
                                    {{ __('اسم المستخدم') }}
                                </label>
                                <span class="text-[10px] text-[#D4AF37] font-bold cursor-pointer hover:underline" onclick="openUserSelectModal()" title="انقر مرتين على الحقل أو هنا للبحث السريع">
                                    (انقر مرتين للبحث) 🔍
                                </span>
                            </div>
                            <div class="relative group">
                                <input id="email" 
                                       type="text" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       required 
                                       autofocus 
                                       ondblclick="openUserSelectModal()"
                                       placeholder="أدخل اسم المستخدم أو الرقم الحسابي"
                                       class="premium-input w-full pr-6 pl-14 text-right cursor-pointer"
                                       autocomplete="username" />
                                <button type="button" 
                                        onclick="openUserSelectModal()" 
                                        tabindex="-1"
                                        class="absolute inset-y-0 left-0 flex items-center pl-5 text-[#D4AF37] hover:scale-110 transition-transform"
                                        title="انقر للبحث والاختيار السريع">
                                    <i data-lucide="user-search" class="w-6 h-6"></i> 
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs text-red-600 font-bold" />
                        </div>

                        <!-- Password Field -->
                        <div class="space-y-3 text-right">
                            <label for="password" class="block text-xs font-black text-gray-500 uppercase tracking-widest mr-1">
                                {{ __('كلمة المرور') }}
                            </label>
                            <div class="relative group">
                                <input id="password" 
                                       type="password"
                                       name="password"
                                       required 
                                       placeholder="••••••••"
                                       class="premium-input w-full pr-6 pl-16 text-right"
                                       autocomplete="current-password" />
                                <button type="button" 
                                        onclick="togglePasswordVisibility()" 
                                        tabindex="-1"
                                        class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 hover:text-[#D4AF37] focus:outline-none transition-colors"
                                        title="إظهار / إخفاء كلمة المرور">
                                    <i id="togglePasswordIcon" data-lucide="eye" class="w-5 h-5"></i>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs text-red-600 font-bold" />
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="flex items-center justify-between mt-6 px-1">
                             <div class="flex items-center">
                                @if (Route::has('password.request'))
                                    <a class="text-xs font-bold text-gray-400 hover:text-gold transition-colors tracking-wide underline underline-offset-4 decoration-gray-200" href="{{ route('password.request') }}">
                                        {{ __('نسيت كلمة المرور؟') }}
                                    </a>
                                @endif
                            </div>

                            <label for="remember_me" class="inline-flex items-center cursor-pointer group flex-row-reverse">
                                <span class="text-xs text-gray-500 font-black group-hover:text-gold transition-colors">{{ __('تذكرني') }}</span>
                                <input id="remember_me" type="checkbox" class="gold-checkbox ml-0 mr-3" name="remember">
                            </label>
                        </div>

                        <div class="pt-6">
                            <button type="submit" id="loginSubmitBtn" class="premium-btn w-full flex items-center justify-center gap-4 group">
                                <i data-lucide="arrow-left" class="w-5 h-5 group-hover:-translate-x-1 transition-transform"></i>
                                <span>{{ __('تسجيل الدخول') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Footer outside the card -->
        <p class="mt-10 text-center text-[10px] text-gray-400 font-black tracking-[0.2em] uppercase">
            © 2026 MOJAWHARATI.PRO. ALL RIGHTS RESERVED.
        </p>
    </div>

    <!-- Quick User Search & Selection Modal -->
    <div id="userSelectModal" class="hidden fixed inset-0 z-[9999] bg-black/80 backdrop-blur-md flex items-center justify-center p-4" dir="rtl">
        <div class="bg-white dark:bg-[#1A1A1A] border-2 border-[#D4AF37] rounded-[32px] shadow-[0_25px_80px_rgba(212,175,55,0.3)] max-w-lg w-full p-6 text-right relative overflow-hidden">
            
            <!-- Header -->
            <div class="flex items-center justify-between border-b border-gray-100 dark:border-white/10 pb-4 mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-[#D4AF37]/10 rounded-full flex items-center justify-center border border-[#D4AF37]">
                        <i data-lucide="users" class="w-5 h-5 text-[#D4AF37]"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-gray-900 dark:text-white" style="font-family: 'Almarai', sans-serif;">
                            اختيار حساب الإدارة للوصول السريع
                        </h3>
                        <p class="text-xs text-gray-400 font-bold">
                            ابحث باسم المستخدم أو بالرقم المتسلسل الحسابي
                        </p>
                    </div>
                </div>
                <button type="button" onclick="closeUserSelectModal()" class="text-gray-400 hover:text-red-500 text-xl font-bold p-1">
                    ✕
                </button>
            </div>

            <!-- Search Field -->
            <div class="relative mb-4">
                <input id="user_search_input" 
                       type="text" 
                       oninput="filterUserList()" 
                       onkeydown="handleUserSearchKeydown(event)"
                       placeholder="اكتب الحرف أو رقم الحساب للفلترة الحساسة..." 
                       class="w-full bg-gray-50 dark:bg-black/40 border border-[#D4AF37]/50 rounded-xl pr-10 pl-4 py-3 text-sm font-bold text-gray-900 dark:text-white focus:border-[#D4AF37] outline-none" />
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 text-[#D4AF37]">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </div>
            </div>

            <!-- User List Items -->
            <div id="user_list_container" class="max-h-72 overflow-y-auto space-y-2 pr-1 custom-scrollbar">
                <!-- Rendered dynamically via JS -->
            </div>

            <div class="mt-4 pt-3 border-t border-gray-100 dark:border-white/10 flex items-center justify-between text-[11px] text-gray-400 font-bold">
                <span>استخدم الأسهم ⬆️ ⬇️ للتنقل ثم <b>Enter</b> للاختيار</span>
                <button type="button" onclick="closeUserSelectModal()" class="px-4 py-1.5 bg-gray-100 dark:bg-white/10 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200">
                    إلغاء
                </button>
            </div>
        </div>
    </div>

    @php
        $rawAdmins = isset($adminUsers) ? $adminUsers : \App\Models\User::whereIn('role', ['super_admin', 'admin', 'moderator', 'support'])->where('status', 'active')->orderBy('user_number', 'asc')->get(['id', 'user_number', 'name', 'email', 'phone', 'role']);
    @endphp

    <script>
        const allAdminUsers = @json($rawAdmins);
        let selectedIndex = 0;
        let filteredUsers = [...allAdminUsers];

        function togglePasswordVisibility() {
            const passInput = document.getElementById('password');
            const icon = document.getElementById('togglePasswordIcon');
            if (passInput.type === 'password') {
                passInput.type = 'text';
                icon.setAttribute('data-lucide', 'eye-off');
            } else {
                passInput.type = 'password';
                icon.setAttribute('data-lucide', 'eye');
            }
            if (window.lucide) window.lucide.createIcons();
        }

        function openUserSelectModal() {
            document.getElementById('userSelectModal').classList.remove('hidden');
            const searchInput = document.getElementById('user_search_input');
            searchInput.value = '';
            filterUserList();
            setTimeout(() => searchInput.focus(), 100);
        }

        function closeUserSelectModal() {
            document.getElementById('userSelectModal').classList.add('hidden');
        }

        function filterUserList() {
            const query = document.getElementById('user_search_input').value.trim().toLowerCase();
            if (!query) {
                filteredUsers = [...allAdminUsers];
            } else {
                filteredUsers = allAdminUsers.filter(u => {
                    const numStr = (u.user_number || u.id || '').toString();
                    const nameStr = (u.name || '').toLowerCase();
                    const emailStr = (u.email || '').toLowerCase();
                    const phoneStr = (u.phone || '').toLowerCase();
                    return numStr.includes(query) || nameStr.includes(query) || emailStr.includes(query) || phoneStr.includes(query);
                });
            }
            selectedIndex = 0;
            renderUserList();
        }

        function renderUserList() {
            const container = document.getElementById('user_list_container');
            container.innerHTML = '';

            if (filteredUsers.length === 0) {
                container.innerHTML = '<div class="p-6 text-center text-gray-400 text-xs font-bold">لا يوجد مستخدم يطابق نص البحث.</div>';
                return;
            }

            filteredUsers.forEach((u, index) => {
                const item = document.createElement('div');
                const isSelected = index === selectedIndex;
                item.className = `p-3.5 rounded-xl border cursor-pointer transition-all flex items-center justify-between ${
                    isSelected 
                        ? 'bg-[#D4AF37]/20 border-[#D4AF37] text-gray-900 dark:text-white shadow-sm' 
                        : 'bg-gray-50 dark:bg-black/20 border-gray-100 dark:border-white/5 hover:border-[#D4AF37]/40'
                }`;
                
                const roleBadge = u.role === 'super_admin' ? 'المدير العام' : (u.role === 'admin' ? 'مدير' : 'مشرف');

                item.innerHTML = `
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-full bg-[#D4AF37] text-black font-black text-xs flex items-center justify-center shadow-sm">
                            #${u.user_number || u.id}
                        </span>
                        <div>
                            <p class="font-black text-sm text-gray-900 dark:text-white leading-tight">${u.name}</p>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 font-bold">${u.email} ${u.phone ? '• ' + u.phone : ''}</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-black px-2.5 py-1 bg-[#D4AF37]/10 text-[#B88F3D] dark:text-[#E8D095] rounded-full border border-[#D4AF37]/30">
                        ${roleBadge}
                    </span>
                `;

                item.onclick = () => selectUser(index);
                container.appendChild(item);
            });

            // Scroll selected item into view
            const activeEl = container.children[selectedIndex];
            if (activeEl) {
                activeEl.scrollIntoView({ block: 'nearest' });
            }
        }

        function handleUserSearchKeydown(e) {
            if (filteredUsers.length === 0) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                selectedIndex = (selectedIndex + 1) % filteredUsers.length;
                renderUserList();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                selectedIndex = (selectedIndex - 1 + filteredUsers.length) % filteredUsers.length;
                renderUserList();
            } else if (e.key === 'Enter') {
                e.preventDefault();
                selectUser(selectedIndex);
            }
        }

        function selectUser(index) {
            const user = filteredUsers[index];
            if (!user) return;

            const emailInput = document.getElementById('email');
            emailInput.value = user.name || user.email;

            closeUserSelectModal();

            // Focus on password input immediately!
            setTimeout(() => {
                const passInput = document.getElementById('password');
                passInput.focus();
                passInput.select();
            }, 100);
        }
    </script>
</x-guest-layout>
