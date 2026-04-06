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
        <div class="bg-white overflow-hidden rounded-[40px] shadow-[0_20px_80px_rgba(0,0,0,0.06)] flex flex-col lg:flex-row min-h-[550px] w-full">
            
            <!-- RIGHT Column (Logo Section) -->
            <div class="w-full lg:w-1/2 p-12 flex flex-col items-center justify-center text-center relative bg-white lg:border-l border-gray-50 order-first">
                <div class="relative flex flex-col items-center">
                    <div class="relative mb-10 w-64 h-64 lg:w-80 lg:h-80 flex items-center justify-center">
                        <div class="absolute inset-0 border-[6px] border-[#D4AF37] rounded-full opacity-20 scale-110"></div>
                        <div class="absolute inset-0 border-2 border-dotted border-[#D4AF37] rounded-full scale-105"></div>
                        <div class="bg-black rounded-full shadow-[0_30px_60px_rgba(0,0,0,0.35)] w-56 h-56 lg:w-72 lg:h-72 flex items-center justify-center">
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
                <div class="max-w-md mx-auto w-full text-right">
                    <div class="mb-12">
                        <h2 class="text-4xl font-black text-gray-900 gold-underline" style="font-family: 'Montserrat', sans-serif;">
                            {{ __('استعادة الحساب') }}
                        </h2>
                        <p class="text-gray-400 font-bold text-sm leading-relaxed mt-4">
                            {{ __('نسيت كلمة المرور؟ لا بأس. أدخل بريدك الإلكتروني وسنرسل لك رابطاً حصرياً لاستعادة الوصول.') }}
                        </p>
                    </div>

                    <!-- Session Status -->
                    <x-auth-session-status class="mb-8" :status="session('status')" />

                    <form method="POST" action="{{ route('password.email') }}" class="space-y-8">
                        @csrf

                        <!-- Email Address -->
                        <div class="space-y-2">
                            <label for="email" class="block text-xs font-black text-gray-500 uppercase tracking-widest mr-1">
                                {{ __('البريد الإلكتروني') }}
                            </label>
                            <div class="relative group">
                                <input id="email" 
                                       type="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       required 
                                       autofocus 
                                       placeholder="admin@mojawharati.com"
                                       class="premium-input w-full pr-6 pl-14 text-right"
                                       autocomplete="username" />
                                <div class="absolute inset-y-0 left-0 flex items-center pl-5 text-[#D4AF37]">
                                    <i data-lucide="gem" class="w-6 h-6"></i>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs" />
                        </div>

                        <div class="pt-6 flex flex-col gap-6">
                            <button type="submit" class="premium-btn w-full flex items-center justify-center gap-4 group">
                                <i data-lucide="send" class="w-5 h-5 group-hover:-translate-x-1 transition-transform"></i>
                                <span>{{ __('إرسال رابط الاستعادة') }}</span>
                            </button>

                            <a href="{{ route('login') }}" class="text-center text-xs font-black text-gray-400 hover:text-gold transition-all duration-300 uppercase tracking-widest flex items-center justify-center gap-2 group">
                                <i data-lucide="chevron-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                                {{ __('العودة لتسجيل الدخول') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
